variable "aws_region" {
  description = "Região AWS utilizada pelo projeto"
  type        = string
  default     = "us-east-1"
}

variable "project_name" {
  description = "Nome do projeto"
  type        = string
  default     = "wordpress-local"
}
